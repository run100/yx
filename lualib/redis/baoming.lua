--
-- 报名 操作脚本
--
local ns = KEYS[1]
local cmdname = ARGV[1]
local function wjerr(code, msg, ...)
    error(string.format('%d: %s', code, string.format(msg, unpack({ ... }))), 2)
end
table.remove(ARGV, 1)  -- remove cmd arg

local kBooked = ns .. ':baoming:booked'
local data_key = ns .. ':data'
local queue_key = ns .. ':queue'
local counter_key = ns .. ':counter'

local cmds = {
    -- 预约
    ['book'] = function(openid, info)
        if redis.call('hexists', kBooked, openid) == 1 then
            wjerr(211, "已预约,不可重复预约")
--            wjerr(211,openid..redis.call('hexists', kBooked, openid))
        end

        redis.call('hset', kBooked, openid, info)
    end,

    ['book_info'] = function(openid, info)
        if redis.call('hexists', kBooked, openid) == 0 then
            wjerr(212, "未查询到预约记录")
        end

        return redis.call('hget', kBooked, openid)
    end,

    register = function(uniqid, data, incr)
        data = cjson.decode(data)
        if redis.call('hexists', data_key, uniqid) == 1 then
            wjerr(34, uniqid .. '已经参与报名')
        end

        if incr then
            data.info.sn = redis.call('incr', counter_key)
        end


        redis.call('hset', data_key, uniqid, cjson.encode(data))
        redis.call('rpush', queue_key, uniqid)

    end,

}

local cmd = cmds[cmdname]
if cmd then
    return cmd(unpack(ARGV))
end

wjerr(91401, 'CMD(%s) not found.', cmdname)
