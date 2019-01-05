--
-- 连接 Redis 库
--

local conf = require('wanjia.conf')
local luacache = require('resty.lrucache').new(100)

local _M = {}

-- 新建链接
_M.new = function()
    local redis = require('resty.redis').new()
    redis:set_timeout(1000)
    redis:connect(conf.REDIS_HOST, conf.REDIS_PORT)

    if redis:get_reused_times() == 0 then
        if conf.REDIS_PASSWORD ~= nil then
            redis:auth(conf.REDIS_PASSWORD)
        end

        if conf.REDIS_DB ~= nil then
            redis:select(conf.REDIS_DB)
        end
    end


    redis.wj_close = function(db)
        db:set_keepalive(10000, 600) -- 关闭链接
    end

    return redis
end

-- 执行 RedisLua 脚本
-- filename: lua 脚本名称(相对路径lialib/redis 目录下，不需要加.lua 后缀)
-- cmd: 脚本内部 command
-- ns:  脚本内部 Key 命名空间
-- ...: 参数列表
_M.exec_lua = function(filename, cmd, ns, ...)
    local redis = _M.new()

    local sha = luacache:get(filename)
    if sha == nil then
        local file = io.open('/var/www/lualib/redis/' .. filename .. '.lua')
        if file ~= nil then
            local script = file:read('*a')
            sha = redis:script('load', script)
            file:close()
            luacache:set(filename, sha)
        end
    end

    if sha == nil then
        return nil
    end

    local ret, err = redis:evalsha(sha, 1, ns, cmd, unpack({ ... }))
    redis:wj_close()

--    if err then
--        return ret, {code=34, msg = err}
--    end


    -- 解析 RedisLua 错误信息
    if err ~= nil then
        err = ngx.re.match(err, [[^ERR Error running script [(]call to f_(?<sha>\w+)[)]: @user_script:(?:\d+): user_script:(?<line>\d+): (?<msg>.+) $]])
        local m = ngx.re.match(err.msg, [[^(\d+): (.+)]])
        if m ~= nil then
            err.code = m[1]
            err.msg = m[2]
        end
        err.file = 'lualib/redis/' .. filename
        err = {
            ["file"] = err.file,
            ["code"] = err.code,
            ["sha"]  = err.sha,
            ["line"] = err.line,
            ["msg"]  = err.msg
        }
    end

    return ret, err
end

return _M


