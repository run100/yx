local _M = {}

_M.strtotime = function(time)
    local pattern = "(%d+)-(%d+)-(%d+) (%d+):(%d+):(%d+)"
    local runyear, runmonth, runday, runhour, runminute, runseconds = time:match(pattern)

    return os.time({year = runyear, month = runmonth, day = runday, hour = runhour, min = runminute, sec = runseconds})
end

_M.in_array = function(needle, hystack)
    if needle and hystack then
        for k, v in ipairs(hystack) do
            if v == needle then
                return true
            end
        end
    end
    return false
end



--[[
-- 2
--]]
_M.pack_error = function(code, msg)
    return {
        code = code,
        msg = msg
    }
end

function _M.print_r(data, as_return)
    local result = {}
    local parent = {}
    local function _print_r(data, indent)
        if type(data) == 'table' then
            for k, v in pairs(data) do
                table.insert(result, string.format('%s%s => %s',
                    indent,
                    type(k) == 'string' and '"'..k..'"' or k,
                    type(v) == 'string' and '"'..v..'"' or v
                )
                )
                if type(v) == 'table' then
                    local p = tostring(data)
                    parent[tostring(v)] = p
                    while true do
                        if p == tostring(v) then
                            table.insert(result, indent .. '    ...')
                            break
                        elseif p == nil then
                            _print_r(v, indent .. '    ')
                            break
                        end
                        p = parent[p]
                    end
                end
            end
        end
    end
    _print_r(data, '')
    result = table.concat(result, '\n')
    if as_return then
        return result
    else
        print(result)
    end
end


function _M.get_session()
    local redis = require('wanjia.redis').new()
    local unserialize = require('wanjia.unserialize').unserialize
    local session_id = ngx.var.cookie_laravel_session
    if not session_id then
        return nil
    end

    local session_key = 'laravel:' .. session_id
    local session_str = redis:get(session_key)
    if not session_str then
        return nil
    end
    local session_obj = unserialize(unserialize(session_str))
    return session_obj
end

function _M.get_openid()
    local session = _M.get_session()
    if not session then
        return nil
    end

    return session.wechat.oauth_user['\x00*\x00attributes'].original.openid
end

return _M