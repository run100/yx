--
-- Redis_test
--

local jm = require('wanjia.json_message')
local redis = require('wanjia.redis').new()

local res = redis:get("foo")
redis:wj_close()

jm.response({
    ['foo'] = res
})
