local checker = require('common.baoming.checker')
local jm = require('wanjia.json_message')
local context = require('common.baoming.context')

local ret, err = checker.check(context, checker.BAOMING)
if err then
    jm.error(err)
end

jm.response('ok')

