--
-- Created by IntelliJ IDEA.
-- User: staff
-- Date: 2017/12/7
-- Time: 上午9:51
-- To change this template use File | Settings | File Templates.
--
-- @TODO 暂不处理字符串中包含；{}

local _M = {}
function _M.unserialize(str)
    local type = string.sub(str, 1, 1)
    if type == 's' then
        local last_colon_index = string.find(str, ':', 3)
        return string.sub(str, last_colon_index + 2, -3)
    end

    if type == 'i' then
        return tonumber(string.sub(str, 3, -2))
    end

    if type == 'b' then
        return string.sub(str, 3, 3) == '1'
    end

    if type == 'N' then
        return nil
    end

    local t = {}
    local length
    if type == 'a' then
        -- print('aaaa')
        -- print(string.find(str, ':', 3) - 3)
        -- print(string.sub(str, 3, 3))
        length = tonumber(string.sub(str, 3, string.find(str, ':', 3) - 1))
    end

    if type == 'O' then
        local search_start = 1
        local colon_index
        for i = 1, 3 do
            colon_index = string.find(str, ':', search_start)
            search_start = colon_index + 1
        end
        length = tonumber(string.sub(str, search_start, string.find(str, ':', search_start+1) -1))
    end

    local start = string.find(str, '{')+1

    for i = 1, length do
        local ends
        local next_opening_bracket_index
        local maybe_ends
        local next_ends

        ends = string.find(str, ';', start)

        maybe_ends = string.find(str, ';', ends+1)
        next_opening_bracket_index = string.find(str, '{', ends+1)
        if maybe_ends and (not next_opening_bracket_index or maybe_ends < next_opening_bracket_index) then
            next_ends = maybe_ends
        else
            local brackets = 1
            local bracket_index = next_opening_bracket_index
            while brackets > 0 do
                bracket_index = string.find(str, '[{}]', bracket_index + 1)
                if string.sub(str, bracket_index, bracket_index) == '{' then
                    brackets = brackets + 1
                else
                    brackets = brackets - 1
                end
            end
            next_ends = bracket_index

        end

        t[_M.unserialize(string.sub(str, start, ends))] = _M.unserialize(string.sub(str, ends + 1, next_ends))
        start = next_ends + 1

    end
    return t

end

return _M

