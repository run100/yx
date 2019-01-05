--
-- 报名:确认
--

local jm = require('wanjia.json_message')
local redis = require('wanjia.redis').new()
local checker = require('common.baoming.checker')
local cjson = require('cjson')
local helper = require('wanjia.helper')
local participant = require('common.model.participant')


local q = ngx.req.get_uri_args()
local post = ngx.req.get_post_args()
local project_id = q.proj
local openid = helper.get_openid()

local ret, err = checker.check(q.proj, checker.CONFIRM, openid)

if err then
    jm.error(err)
end

local conf = cjson.decode(redis:get('prj:1:conf'))
post.openid = openid
local ret, err = participant.update_participant(post, project_id, openid, conf)
if err then
    jm.error(err)
end

jm.response('ok')



