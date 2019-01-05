--
-- 读取 .env 配置，并存储到 ngx.shared.wanjia_conf
-- .env 文件的修改对于 nginx 需要执行 nginx -s reload 才会重新加载(所有的 lua 脚本均如此)
--

if ngx.shared.wanjia_conf ~= nil then
    return ngx.shared.wanjia_conf
end

local file = io.open('/var/www/.env', 'r')
ngx.shared.wanjia_conf = {}

local line
while true
do
    line = file:read('*l')
    if line == nil then
        break
    end

    local pos = line:find('=')
    if pos ~= nil then
        local k = line:sub(0, pos - 1)
        local v = line:sub(pos + 1)
        if v == 'true' then
            v = true
        elseif v == 'false' then
            v = false
        elseif v == 'null' then
            v = nil
        end
        ngx.shared.wanjia_conf[k] = v
    end
end

file:close()
return ngx.shared.wanjia_conf
