--
-- 报名:预约
--
local jm = require('wanjia.json_message')
local proj = require('wanjia.project')
local redis = require('wanjia.redis')
local checker = require('common.baoming.checker')
local helper = require('wanjia.helper')
local q = ngx.req.get_uri_args()
local conf = proj.conf(q.proj)

local openid = helper.get_openid()

local ret, err = checker.check(q.proj, checker.BOOK, q.openid)
if err then
    jm.error(err)
end

ret, err = redis.exec_lua('baoming', 'book', conf.ns, openid, 1)
if err then
--    jm.response(err.msg)
    jm.error(err)
end

jm.response('ok')
