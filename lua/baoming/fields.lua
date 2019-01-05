--
-- Created by IntelliJ IDEA.
-- User: staff
-- Date: 2017/12/13
-- Time: 下午5:04
-- To change this template use File | Settings | File Templates.
--
local redis_conn = require('wanjia.redis').new()
local jm = require('wanjia.json_message')
local cjson = require('cjson')
local query_string = ngx.req.get_uri_args()
local project_id = query_string.proj
local errors = require('common.errors')

local key = string.format('prj:%d:conf', project_id)
if redis_conn:exists(key) == 0 then
    jm.error(errors.error(errors.PROJECT_NOT_FOUND))
end

local conf = redis_conn:get(key)

jm.response(cjson.encode(cjson.decode(conf).base_form_design))
--jm.error({code=23,msg='wrong'})

