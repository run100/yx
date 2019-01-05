--
-- 报名:查询
--
local jm = require('wanjia.json_message')
local proj = require('wanjia.project')

local q = ngx.req.get_uri_args()
local conf = proj.conf(q.proj)

