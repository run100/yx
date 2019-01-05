local ns = KEYS[1]
local cmdname = ARGV[1]
local kHbcount = ns .. ':hbcount'
local kHbmoney = ns .. ':hbmoney'
local kPlayers = ns .. ':players'
local kWins = ns .. ':wins'
local kHblogs = ns .. ':hblogs'

local function wjerr(code, msg, ...)
    error(string.format('%d: %s', code, string.format(msg, unpack({ ... }))), 2)
end
table.remove(ARGV, 1)


local cmds = {
    luaGiveRedpacket = function(money, totalMoney, totalCount, isYes, pid, drawCount, time, uniqId)
        local user = cjson.decode(redis.call('hget', kPlayers, pid))
        if tonumber(drawCount)<= user['count'] then
            return -2
        end
        money = tonumber(money)
        if money == 0 and user['money'] then
            money = tonumber(user['money'])
        end
        if (tonumber(totalMoney)) <= (tonumber(redis.call('get', kHbmoney)) + money) then
            user['count'] = user['count']+1
            user['reset'] = 1
            redis.call('hset', kPlayers, pid, cjson.encode(user))
            return -1
        end
        isYes = tonumber(isYes);
        if isYes == 1 and tonumber(totalCount) <= tonumber(redis.call('get', kHbcount)) then
            user['count'] = user['count']+1
            user['reset'] = 1
            redis.call('hset', kPlayers, pid, cjson.encode(user))
            return -1
        end
        redis.call('incrby', kHbmoney, money);
        redis.call('incr', kHbcount);
        user['count'] = user['count']+1
        local wins = (user['wins'] and user['wins']) or {}
        wins[(#wins+1)] = {t=time, m=money}
        user['wins'] = wins
        user['reset'] = 1
        redis.call('hset', kPlayers, pid, cjson.encode(user))
        redis.call('zadd', kWins, time, '{"n":"'..user['name']..'","m":"'..money..'","i":"'..uniqId..'"}')
        return 1
    end,
    luaUpdatePlayerCount = function(pid, drawCount)
        local user = cjson.decode(redis.call('hget', kPlayers, pid))
        if tonumber(drawCount) <= user['count'] then
            return -2
        end
        user['count'] = user['count']+1
        redis.call('hset', kPlayers, pid, cjson.encode(user));
        return 1
    end,
    luaZhuli = function(zlid, pid, zdlimit, drawCount, poster)
        if tonumber(redis.call('sismember', kHblogs, zlid)) == 1 then
            return -3
        end
        local user = cjson.decode(redis.call('hget', kPlayers, pid))
        local zls = (user['zls'] and user['zls']) or {}
        local zdcount = #zls
        if tonumber(drawCount) <= user['count'] then
            return -2
        end
        if (tonumber(zdlimit)) <= zdcount then
            return -1
        end
        zdcount = zdcount+1
        local m = (user['moneys'][zdcount] and user['moneys'][zdcount]) or 0
        zls[zdcount] = {p=poster, m=m }
        user['zls'] = zls
        redis.call('hset', kPlayers, pid, cjson.encode(user))
        redis.call('sadd', kHblogs, zlid)
        return user['name']..'|wjhb6|'..m
    end
}
local cmd = cmds[cmdname]
if cmd then
    return cmd(unpack(ARGV))
end
wjerr(91401, 'CMD(%s) not found.', cmdname)