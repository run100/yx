--
-- DB_test
--
local cjson = require('cjson')
local redis = require('wanjia.redis').new()
local helper = require('wanjia.helper')
local session_obj = helper.get_session()
--ngx.say(helper.print_r(session_obj, true))
--ngx.say(session_obj)

local jm = require('wanjia.json_message')
local db = require('wanjia.db').new()

--local res = db:query("select * from zt_project where 1=2")
ngx.say(helper.get_openid())
ngx.exit(200)
db:wj_close()

jm.response_page(res, 20, 1, 10, {['muhaha'] = 'muhaha'})
