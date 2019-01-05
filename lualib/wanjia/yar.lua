--
-- 支持用 Lua 写 Yar 服务
--

local cjson = require('cjson')
local _M = {}

_M.parse = function()
    ngx.req.read_body()
    local body = ngx.req.get_body_data()
    local s = cjson.decode(body:sub(91))

    return {
        ['head']    = body:sub(1, 82),
        ['packager']= body:sub(83, 90),
        ['transaction_id']    = s.i,
        ['method']            = s.m,
        ['args']              = s.p
    }
end

_M.response = function(req, data)
    local msg = {}
    msg.i = req.transaction_id
    msg.s = ""
    msg.r = data
    msg.o = ""
    msg.e = ""
    ngx.say(table.concat({req.head, req.packager, cjson.encode(msg)}))
end

return _M