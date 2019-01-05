local ns = KEYS[1]
local cmdname = ARGV[1]
local kTotal = ns .. ':lmt_log_t'
local kLimit = ns .. ':lmt_log_d_'
local kPlyLimit = ns .. ':lmt_ply_d_'

local function wjerr(code, msg, ...)
    error(string.format('%d: %s', code, string.format(msg, unpack({ ... }))), 2)
end
table.remove(ARGV, 1)

local cmds = {
    checkLimit = function(total, limit, plyLimit, postfix, openid, plyId, time)
        total = tonumber(total);
        limit = tonumber(limit);
        plyLimit = tonumber(plyLimit);
        local results = {0, 0, 0};
        if total>0 then
            results[1] = 1;
            local totalCount = redis.call('hget', kTotal, openid)
            if totalCount and tonumber(totalCount) >= total then
                return -1
            end
        end
        if limit>0 then
            results[2] = 1;
            kLimit = kLimit..postfix
            local limitCount = redis.call('hget', kLimit, openid)
            if limitCount and tonumber(limitCount) >= limit then
                return -2
            end
        end
        if plyLimit>0 then
            results[3] = 1;
            kPlyLimit = kPlyLimit..postfix;
            local plyLimitCount = redis.call('hget', kPlyLimit, plyId)
            if plyLimitCount and tonumber(plyLimitCount) >= plyLimit then
                return -3
            end
        end
        if results[1] == 1 then
            redis.call('hincrby', kTotal, openid, 1)
        end
        if results[2] == 1 then
            if tonumber(redis.call('exists', kLimit)) == 1 then
                redis.call('hincrby', kLimit, openid, 1)
            else
                redis.call('hincrby', kLimit, openid, 1)
                redis.call('expire', kLimit, time)
            end
        end
        if results[3] == 1 then
            if tonumber(redis.call('exists', kPlyLimit)) == 1 then
                redis.call('hincrby', kPlyLimit, plyId, 1)
            else
                redis.call('hincrby', kPlyLimit, plyId, 1)
                redis.call('expire', kPlyLimit, time)
            end
        end
        return 0
    end
}
local cmd = cmds[cmdname]
if cmd then
    return cmd(unpack(ARGV))
end
wjerr(91401, 'CMD(%s) not found.', cmdname)