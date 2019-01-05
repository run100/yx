--
-- 报名
--
local jm = require('wanjia.json_message')
local redis = require('wanjia.redis')
local redis_conn = redis.new()
local checker = require('common.baoming.checker')
local cjson = require('cjson')
local helper = require('wanjia.helper')
local participant = require('common.model.participant')
local context = require('common.baoming.context')


local post = ngx.req.get_post_args()

local ret, err = checker.check(context, checker.BAOMING)

if err then
    jm.error(err)
end

local data = cjson.decode(post.data)
for k, v in pairs(data) do
    local ret, err = participant.save_participant(context, v)
    if err then
        jm.error(err)
    end
end


jm.response('ok')



