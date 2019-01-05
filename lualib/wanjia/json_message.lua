--
-- JsonMessage
--

local cjson = require('cjson')
local _M = {}

-- 返回 json_message 结构(不输出)
-- data: 数据内容
-- code: 错误代码,0表示成功(默认)
_M.pack = function(data, code)
    if code == nil then
        code = 0
    end

    local msg = {}

    if code ~= 0 and type(data) == 'string' then
        msg.code = code
        msg.msg = data
    else
        msg.code = code
        msg.data = data
        if type(data) == 'string' then
            msg.msg = data
        elseif type(data) == 'table' and data.msg ~= nil then
            msg.msg = data.msg
        end
        msg.httpStatusCode = 200
    end

    cjson.encode_sparse_array(true, 1, 1)
    return cjson.encode(msg)
end

-- 返回 json_message page结构(不输出)
-- data: list数据
-- total: 总行数
-- page: 当前页码
-- perpage: 页大小
-- extras: table类型附加数据
_M.page = function(data, total, page, perpage, extras)
    local msg = {}

    if extras == nil then
        extras = {}
    end

    if perpage == nil then
        perpage = 20
    end

    if total == nil then
        total = 0
    end

    if page == nil then
        page = 1
    end



    msg.page = page
    msg.total = total
    msg.perpage = perpage
    msg.max_page = math.floor((total - 2) / perpage) + 1
    msg.has_more = page < msg.max_page
    msg.extras = extras
    msg.data = data

    return _M.pack(msg)
end

-- 输出 json_message 并终止请求
_M.response = function(...)
    ngx.header['Content-Type'] = 'application/javascript; charset=utf-8'
    ngx.say(_M.pack(unpack({ ... })))
    ngx.exit(200)
end

-- 输出 json_message/page 结构 并终止请求
_M.response_page = function(...)
    ngx.header['Content-Type'] = 'application/javascript; charset=utf-8'
    ngx.say(_M.page(unpack({ ... })))
    ngx.exit(200)
end

-- 输出 错误内容 并终止请求
-- table类型, 必填字段 code/msg
_M.error = function(err)
    _M.response(err, tonumber(err.code))
end



return _M