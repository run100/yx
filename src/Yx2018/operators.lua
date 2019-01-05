--
-- 脚本说明。。。。。。
--
--

-- 头部固定部分
local ns = KEYS[1]
local cmdname = ARGV[1]
local function wjerr(code, msg, ...)
    error(string.format('%d: %s', code, string.format(msg, unpack({ ... }))), 2)
end
table.remove(ARGV, 1)  -- remove cmd arg
-- 头部固定部分End

-- Key定义部分
local kPlayers = ns .. ':players'               -- 选手表            {Hash}           Phone  => 选手信息JSON
local kGroups = ns .. ':groups'                 -- 参赛组            {Hash}           OpenID => Phone列表逗号分割，第一个为领队
local kGroupLines = ns .. ':group_lines'        -- 参赛租线路        {Hash}           OpenId => Line
local kWxMembers = ns .. ':wx_members'          -- 微信用户          {Hash}           OpenID => 微信用户信息JSON(头像、昵称)
local kPassports = ns .. ':passports'           -- 参赛证件          {Set}            {Passport_Type}:{Passport}
local pVoteLog = ns .. ':vote_log:'             -- 助力记录(多值前缀) {OpenId:List}    时间戳,OpenId,捐款数额
local pVoteLimit = ns .. ':vote_limit:'         -- 助力限制(多值前缀) {OpenId:Set}     助力者OpenId
local pRanking = ns .. ':rank:'                 -- 排行榜(多值前缀)   {线路:SortedSet} OpenID => {整数部分=总金额}.{小数部分=X-时间戳}
local kThreads = ns .. ':threads'               -- thread列表       {Hash}           threadid => 报名者OpenID
local kTotalDonate = ns .. ':total_donate'      -- 总捐助金额        {String}
local kNicknamesNew = ns .. ':nicknamesnew'     -- 用于昵称搜索      {Hash}           nickname => threadid,headimgurl
local kWaterLine = ns .. ':water_line'          -- 名额预测          {Hash}           line => rank
local kSearchPlayer = ns .. ':search_player'    -- 用于选手查询       {Hash}          Hash(name, passport) => phone
-- Key定义部分End

-- 逻辑定义部分
local cmds = {
    -- 报名
    regist = function(threadid, openid, players)

        local ret

        ret = redis.call('hexists', kGroups, openid)
        if tonumber(ret) > 0 then
            return {"ERR_OPENID", "抱歉，每个微信号只能报名一次"}
        end

        local players = cjson.decode(players)

        if #players <= 0 then
            return {"ERR_PLAYERS_SIZE", "抱歉，参数无效" }
        end

        for i = 1, #players do
            local player = players[i]
            ret = redis.call('hexists', kPlayers, player.phone)
            if tonumber(ret) > 0 then
                return {"ERR_PHONE", string.format("抱歉，每个手机号只能参与一次报名，%s的手机号已经报过了", player.name)}
            end

            ret = redis.call('sismember', kPassports, player.passport)
            if tonumber(ret) > 0 then
                return {"ERR_PASSPORT", string.format("抱歉，每个证件号只能参与一次报名，%s的证件号已经报过了", player.name)}
            end
        end


        local player_list = ''
        local line
        for i = 1, #players do
            local player = players[i]
            line = player.line
            redis.call('hset', kPlayers, player.phone, cjson.encode(player))

            if player.hash then
                redis.call('hset', kSearchPlayer, player.hash, player.phone)
            end

            redis.call('sadd', kPassports, player.passport)
            player_list = string.format("%s,%s", player_list, player.phone)
        end

        local info = redis.call('hget', kWxMembers, openid)
        if info then
            info = cjson.decode(info)
            local nickname = info.nickname:lower()
            local openid_list = redis.call('hget', kNicknamesNew, nickname)
            if openid_list then
                openid_list = openid_list .. ',' .. openid;
            else
                openid_list = openid;
            end
            redis.call('hset', kNicknamesNew, nickname, openid_list)
        end

        redis.call('hset', kGroups, openid, player_list:sub(2))
        redis.call('hset', kGroupLines, openid, line)
        redis.call('hset', kThreads, threadid, openid)

        return {"SUCCESS", "报名成功"}
    end,

    -- 删除选手
    removePlayer = function(phone)
        local player = redis.call('hget', kPlayers, phone)
        if not player then
            return true
        end

        player = cjson.decode(player)
        -- 从Group中移除
        if player.openid ~= '' then
            local members = redis.call('hget', kGroups, player.openid)
            local player_list = ''
            if members then
                for pos = 1, #members, 12 do
                    local v = members:sub(pos, pos + 10)
                    if v ~= phone then
                        player_list = string.format("%s,%s", player_list, v)
                    end
                end

                redis.call('hset', kGroups, player.openid, player_list:sub(2))
            end
        end

        -- 清除Passport占用状态
        redis.call('srem', kPassports, player.passport)

        if player.hash then
            redis.call('hdel', kSearchPlayer, player.hash)
        end

        -- 删除
        redis.call('hdel', kPlayers, phone)

    end,

    -- 更新/保存选手信息
    savePlayer = function(info)
        local player = cjson.decode(info)
        redis.call('hset', kPlayers, player.phone, info)
        redis.call('sadd', kPassports, player.passport)

        if player.hash then
            redis.call('hset', kSearchPlayer, player.hash, player.phone)
        end

        -- 加入到Group
        if player.openid ~= '' then
            local members = redis.call('hget', kGroups, player.openid)
            if members and #members > 0 then
                if player.is_master == 'Y' then
                    redis.call('hset', kGroups, player.openid, player.phone .. ',' .. members)
                else
                    redis.call('hset', kGroups, player.openid, members .. ',' .. player.phone)
                end
            else
                redis.call('hset', kGroups, player.openid, player.phone)
            end
        end
    end,

    -- 检查Group是否存在
    getGroup = function(groupid)

        local ret = {}

        local line = redis.call('hget', kGroupLines, groupid)
        if line then
            ret[#ret + 1] = 'ranking'
            local kRanking = pRanking .. line
            local v = redis.call('zrevrank', kRanking, groupid)
            if v then
                ret[#ret + 1] = v + 1
            else
                ret[#ret + 1] = 0
            end

            ret[#ret + 1] = 'donate'
            ret[#ret + 1] = redis.call('zscore', kRanking, groupid)
        else
            ret[#ret + 1] = 'ranking'
            ret[#ret + 1] = 0

            ret[#ret + 1] = 'donate'
            ret[#ret + 1] = 0
        end


        local members = redis.call('hget', kGroups, groupid)
        local tMembers = {}
        ret[#ret + 1] = 'players'
        if members then
            for pos = 1, #members, 12 do
                local v = members:sub(pos, pos + 10)
                tMembers[#tMembers + 1] = redis.call('hget', kPlayers, v)
            end
            ret[#ret + 1] = tMembers
        else
            ret[#ret + 1] = false
        end

        ret[#ret + 1] = '--HASH_END--'

        return ret
    end,

    vote = function(groupid, openid, score, timestamp)
        local line = redis.call('hget', kGroupLines, groupid)
        redis.call('zincrby', pRanking .. line, score, groupid)
        redis.call('sadd', pVoteLimit .. groupid, openid)
        redis.call('lpush', pVoteLog .. groupid, string.format('%s,%s,%s', timestamp, openid, score))

        local donate = math.floor(score)
        if donate >= 1 then
            redis.call('incrby', kTotalDonate, donate)
        end
    end,

    getVotelist = function(groupid, pos, len)
        local stop
        if tonumber(len) >= 0 then
            stop = pos + len - 1
        else
            stop = len
        end

        local kVoteLog = pVoteLog .. groupid;
        local total = redis.call('llen', kVoteLog)

        if total - 2 < 0 then
            return false
        end

        -- 倒序排列，最后一条是时间记录，不算
        if stop > total - 2 then
            stop = total - 2
        end

        local arr = {}
        local arr0 = redis.call('lrange', kVoteLog, pos, stop)
        for i = 1, #arr0 do
            local item = {}
            item[#item + 1] = 'time'
            item[#item + 1] = arr0[i]:sub(1, 10)

            local openid = arr0[i]:sub(12, 39)
            item[#item + 1] = 'openid'
            item[#item + 1] =openid

            item[#item + 1] = 'donate'
            item[#item + 1] = arr0[i]:sub(41)

            item[#item + 1] = 'info'
            item[#item + 1] = redis.call('hget', kWxMembers, openid)

            item[#item + 1] = '--HASH_END--'

            arr[#arr + 1] = item
        end

        local ret = {}

        ret[#ret + 1] = 'data'
        ret[#ret + 1] = arr
        ret[#ret + 1] = 'has_more'
        ret[#ret + 1] = stop < total - 2
        ret[#ret + 1] = '--HASH_END--'

        return ret
    end,

    ranking = function(line, pos, len, withme, around_n)
        local stop
        if tonumber(len) >= 0 then
            stop = pos + len - 1
        else
            stop = len
        end

        local ret = {}

        local kRanking = pRanking .. line
        local ranking = redis.call('zrevrange', kRanking, pos, stop, 'withscores')

        local groups = {}
        for i = 1, #ranking, 2 do
            local openid = ranking[i]
            local group = {}
            local players = {}
            local members = redis.call('hget', kGroups, openid)
            for pos = 1, #members, 12 do
                local member = members:sub(pos, pos + 10)
                players[#players + 1] = redis.call('hget', kPlayers, member)
            end

            group[#group + 1] = 'players'
            group[#group + 1] = players

            group[#group + 1] = 'info'
            group[#group + 1] = redis.call('hget', kWxMembers, openid)

            group[#group + 1] = 'score'
            group[#group + 1] = ranking[i + 1]

            group[#group + 1] = 'rank'
            group[#group + 1] = (i + 1) / 2

            group[#group + 1] = 'openid'
            group[#group + 1] = openid
            group[#group + 1] = '--HASH_END--'

            groups[#groups + 1] = group
        end

        ret[#ret + 1] = 'ranking'
        ret[#ret + 1] = groups

        local topN = groups;

        --我的排名
        if withme then
            if not around_n then
                around_n = 10
            end

            local rank = redis.call('zrevrank', kRanking, withme)
            if rank then
                pos = rank - around_n
                if pos < 0 then
                    pos = 0
                end
                stop = rank + around_n

                ranking = redis.call('zrevrange', kRanking, pos, stop, 'withscores')

                groups = {}
                for i = 1, #ranking, 2 do
                    local openid = ranking[i]
                    local group = {}
                    local players = {}
                    local members = redis.call('hget', kGroups, openid)
                    for pos = 1, #members, 12 do
                        local member = members:sub(pos, pos + 10)
                        players[#players + 1] = redis.call('hget', kPlayers, member)
                    end

                    group[#group + 1] = 'players'
                    group[#group + 1] = players

                    group[#group + 1] = 'info'
                    group[#group + 1] = redis.call('hget', kWxMembers, openid)

                    group[#group + 1] = 'score'
                    group[#group + 1] = ranking[i + 1]

                    group[#group + 1] = 'rank'
                    group[#group + 1] = pos + (i + 1) / 2

                    group[#group + 1] = 'openid'
                    group[#group + 1] = openid
                    group[#group + 1] = '--HASH_END--'

                    groups[#groups + 1] = group

                    if openid == withme then
                        ret[#ret + 1] = 'me'
                        ret[#ret + 1] = group
                    end
                end
                ret[#ret + 1] = 'rank'
                ret[#ret + 1] = rank + 1
            end
            ret[#ret + 1] = 'around'
            ret[#ret + 1] = groups
        else
            return topN
        end

        ret[#ret + 1] = '--HASH_END--'
        return ret
    end,

    -- 清空数据
    destroy = function()
        redis.call('del', kGroupLines, kGroups, kWxMembers, kPlayers, kPassports, kThreads, kTotalDonate, kNicknamesNew, kWaterLine, kSearchPlayer)

        local arr = redis.call('keys', pRanking .. '*')
        for i = 1, #arr do
            redis.call('del', arr[i])
        end

        arr = redis.call('keys', pVoteLog .. '*')
        for i = 1, #arr do
            redis.call('del', arr[i])
        end

        arr = redis.call('keys', pVoteLimit .. '*')
        for i = 1, #arr do
            redis.call('del', arr[i])
        end

    end,

    -- 数据完整性检查
    checkData = function()
        local nGroups = tonumber(redis.call('hlen', kGroups));
        local nGroupLines = tonumber(redis.call('hlen', kGroupLines));
        if nGroups ~= nGroupLines then
            return {"ERR_GROUP_LEAKED", string.format("Group/GroupLine数据缺失")}
        end

        local nPlayers = tonumber(redis.call('hlen', kPlayers));
        local nPassports = tonumber(redis.call('scard', kPassports));
        if nPlayers ~= nPassports then
            return {"ERR_PASSPORT_LEAKED", string.format("Passport数据检查失败")}
        end


        local groups = redis.call('hvals', kGroups)
        local ngPlayers = 0
        for pos0 = 1, #groups do
            local members = groups[pos0]
            local cntMembers = 0;
            for pos = 1, #members, 12 do
                local phone = members:sub(pos, pos + 10)
                local player = redis.call('hget', kPlayers, phone)
                if not player then
                    return {"ERR_PLAYER_LEAKED1", string.format("Player数据丢失，Phone: %s", phone)}
                end
                cntMembers = cntMembers + 1
            end
            ngPlayers = ngPlayers + cntMembers
        end

        if nPlayers ~= ngPlayers then
            return {"ERR_PASSPORT_LEAKED2", string.format("Group中Phone数据丢失")}
        end

        return {"OK", nPlayers}
    end
}
-- 逻辑定义部分End

-- 尾部固定部分
local cmd = cmds[cmdname]
if cmd then
    return cmd(unpack(ARGV))
end
wjerr(91401, 'CMD(%s) not found.', cmdname)
-- 尾部固定部分End



