local ns = KEYS[1]
local cmdname = ARGV[1]
local kPlayer = ns .. ':bg:'
local kTotalCount = ns .. ':bgcount'
local kRaking = ns .. ':ranking'

local function wjerr(code, msg, ...)
    error(string.format('%d: %s', code, string.format(msg, unpack({ ... }))), 2)
end
table.remove(ARGV, 1)

local cmds = {
    luaBargain = function(bargainPrice, max, ticketNo, maxCount, target, nickName, poster)
        bargainPrice = tonumber(bargainPrice);
        maxCount = tonumber(maxCount);
        target = tonumber(target);
        max = tonumber(max);
        local totalCount = redis.call('get', kTotalCount);
        if totalCount then
            totalCount = tonumber(totalCount);
        else
            totalCount = 0;
        end
        if totalCount >= maxCount then
            return {-1};
        end
        local key = kPlayer..ticketNo;
        local userPrice = tonumber(redis.call('hget', key, 'price'));
        if userPrice<=target then
            return {-2};
        end
        local cha = userPrice - target;
        if cha<=max then
            bargainPrice = cha;
        end
        local newPrice = tonumber(redis.call('hincrby', key, 'price', -bargainPrice));
        local score = newPrice;
        if newPrice == 0 then
            local paiming = redis.call('incr', kTotalCount);
            score = maxCount+1-paiming;
        end
        local oldObj = {};
        oldObj['name'] = nickName;
        oldObj['poster'] = poster;
        oldObj['price'] = userPrice;
        redis.call('zrem', kRaking, cjson.encode(oldObj));
        oldObj['price'] = newPrice;
        redis.call('zadd', kRaking, score, cjson.encode(oldObj));
        return {1, bargainPrice, newPrice}
    end,
    luaAddSet = function(nickName, poster, price)
        local oldObj = {};
        oldObj['name'] = nickName;
        oldObj['poster'] = poster;
        oldObj['price'] = tonumber(price);
        redis.call('zadd', kRaking, price, cjson.encode(oldObj));
    end
}
local cmd = cmds[cmdname]
if cmd then
    return cmd(unpack(ARGV))
end
wjerr(91401, 'CMD(%s) not found.', cmdname)