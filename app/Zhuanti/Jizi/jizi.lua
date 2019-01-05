local ns = KEYS[1]
local cmdname = ARGV[1]
local kJizis = ns .. ':jizis'
local kPlayerJizi = ns .. ':pjz:'

local function wjerr(code, msg, ...)
    error(string.format('%d: %s', code, string.format(msg, unpack({ ... }))), 2)
end
table.remove(ARGV, 1)

local cmds = {
    luaGiveFont = function(font, total, bfont, ticketNo)
        total = tonumber(total)
        local isBX = 0
        local key = font
        if font ~= '' then
            local count = tonumber(redis.call('hget', kJizis, font))
            if count >= total then
                isBX = 1
                key = bfont
            end
        else
            isBX = 1
            key = bfont
        end
        redis.call('hincrby', kPlayerJizi..ticketNo, key, 1)
        return {isBX, redis.call('hincrby', kJizis, key, 1)}
    end
}
local cmd = cmds[cmdname]
if cmd then
    return cmd(unpack(ARGV))
end
wjerr(91401, 'CMD(%s) not found.', cmdname)