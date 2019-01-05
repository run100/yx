--
-- 报名:排队逻辑
--
local jm = require('wanjia.json_message')
local proj = require('wanjia.project')

local q = ngx.req.get_uri_args()
local conf = proj.conf(q.proj)

-- 预约
if q.act == 'book' then
    jm.response('ok')
end

-- 查询
if q.act == 'query' then
    jm.response('ok')
end

-- 报名
if q.act == 'baoming' then
    if conf.baoming.can_queue == 0 then
        jm.response('ok')
    end

    local rand = math.random(0, 1000000)
    local seed = rand / 1000000

    if seed <= conf.baoming.queue_rate / 100 then
        jm.response('ok')
    else
        jm.response('continue', 28)
    end
end



