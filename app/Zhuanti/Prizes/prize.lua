local ns = KEYS[1]
local cmdname = ARGV[1]
local kPrizes = ns .. ':prizes'

local function wjerr(code, msg, ...)
    error(string.format('%d: %s', code, string.format(msg, unpack({ ... }))), 2)
end
table.remove(ARGV, 1)

local cmds = {
    luaGivePrize = function(prize, total, bPrize)
        local isBX = 0
        if prize ~= '' then
            if tonumber(total) <= tonumber(redis.call('hget', kPrizes, prize)) then
                isBX = 1
                prize = bPrize
            end
        else
            isBX = 1
            prize = bPrize
        end
        return {isBX, redis.call('hincrby', kPrizes, prize, 1)}
    end
}
local cmd = cmds[cmdname]
if cmd then
    return cmd(unpack(ARGV))
end
wjerr(91401, 'CMD(%s) not found.', cmdname)