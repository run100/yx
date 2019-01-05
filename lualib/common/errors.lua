--
-- Created by IntelliJ IDEA.
-- User: staff
-- Date: 2017/12/15
-- Time: 下午5:12
-- To change this template use File | Settings | File Templates.
--

local errors = {}

errors.PROJECT_NOT_FOUND = 201001
errors.PROJECT_NOT_ENABLED = 201002
errors.PROJECT_NOT_STARTED = 201003
errors.PROJECT_EXPIRED = 201004

errors.ACTION_NOT_SUPPORTED = 201005
errors.QUOTA_EXCEEDED = 201006
errors.REGISTER_STEP_NOT_SUPPORTED = 201007
errors.REGISTER_TYPE_NOT_SUPPORTED = 201008
errors.TIME_BASED_EVENT = 201009
errors.EVENT = 201010

errors.FIELD_MISSING = 202001
errors.FIELD_DUPLICATED = 202002
errors.FIELD_INVALID = 202003


errors.DATABASE_ERROR = 102000

errors.message = {
    [errors.PROJECT_NOT_FOUND] = '项目不存在',
    [errors.PROJECT_NOT_ENABLED] = '项目未启用',
    [errors.PROJECT_NOT_STARTED] = '项目未开始',
    [errors.PROJECT_EXPIRED] = '项目已结束',

    [errors.ACTION_NOT_SUPPORTED] = '不支持当前操作',
    [errors.QUOTA_EXCEEDED] = '名额超限',
    [errors.REGISTER_STEP_NOT_SUPPORTED] = '不支持该步骤',
    [errors.TIME_BASED_EVENT] = '时间事件',
    [errors.EVENT ] = '事件',


    [errors.FIELD_MISSING] = '字段缺失',
    [errors.FIELD_DUPLICATED] = '字段重复',
    [errors.FIELD_INVALID] = '格式错误',


    [errors.DATABASE_ERROR] = '出错了',
}

function errors.error(code, message)
    if message then
        return {code=code, msg=message }
    end

    if errors.message[code] then
        return {code=code, msg=errors.message[code] }
    end

    return {code=code, msg='错误'}

end

return errors
