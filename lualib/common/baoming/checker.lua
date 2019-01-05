local _M = {}

_M.BOOK = 'book'
_M.BAOMING = 'baoming'
_M.QUERY = 'query'
_M.CONFIRM = 'confirm'

_M.steps = {
    [_M.BOOK] = '预约',
    [_M.BAOMING] = '报名',
    [_M.QUERY] = '查询',
    [_M.CONFIRM] = '确认'
}

function _M.check(context, step)
    local errors = require('common.errors')
    local helper = require('wanjia.helper')
    local cjson = require('cjson')
    local redis = require('wanjia.redis').new()
    local proj = context.get_project_info();
    local id = context.get_project_id();

    if  not proj then
        return false, errors.error(errors.PROJECT_NOT_FOUND)
    end

    if proj.is_enabled == 0 then
        return false, errors.error(errors.PROJECT_NOT_ENABLED)
    end

    if helper.strtotime(proj.start_at) >= ngx.time() then
        return false, errors.error(errors.PROJECT_NOT_STARTED)
    end

    if helper.strtotime(proj.end_at) <= ngx.time() then
        return false, errors.error(errors.PROJECT_EXPIRED)
    end

    if not string.find(proj.capacity, 'baoming') then
        return false, errors.error(errors.ACTION_NOT_SUPPORTED)
    end

    local config = cjson.decode(proj.configs)

    if not helper.in_array(step, config.baoming.step_mode) then
        return false, errors.error(errors.REGISTER_STEP_NOT_SUPPORTED, '该项目不支持' .. _M.steps[step])
    end


    if context.get_register_type == 'single' and config.baoming.can_single ~= 1 then
        return false, errors.error(errors.REGISTER_TYPE_NOT_SUPPORTED, '该项目不支持单人报名')
    end

    if context.get_register_type == 'team' and config.baoming.can_group ~= 1 then
        return false, errors.error(errors.REGISTER_TYPE_NOT_SUPPORTED, '该项目不支持团队报名')
    end







    for k, event in pairs(config.baoming.time_msgs) do
        if event.enable
                and helper.in_array(step, event.step_mode)
                and (event.start == nil or helper.strtotime(event.start) <= ngx.time())
                and (event['end'] == nil or helper.strtotime(event['end']) >= ngx.time()) then
            return false, errors.error(errors.TIME_BASED_EVENT, event.msg)
        end
    end



    if step ~= _M.BAOMING then
        return true
    end

    if config.baoming.can_limit == 1 and context.get_registered() >= tonumber(config.baoming.limits) then
        for k, event in pairs(config.baoming.event_msgs) do
            if event.enable
                    and event.event == 'over_limit'
                    and (event.start == nil or helper.strtotime(event.start) <= ngx.time())
                    and (event['end'] == nil or helper.strtotime(event['end']) >= ngx.time()) then
                return false, errors.error(errors.EVENT, errors.message[errors.QUOTA_EXCEEDED])
            end
        end
        return false, errors.error(errors.QUOTA_EXCEEDED)
    end

    local kBooked = string.format('prj:%d:baoming:booked', id)
    if helper.in_array(_M.BOOK, config.baoming.step_mode)
            and redis:hexists(kBooked, openid) == 0 then
        return false, errors.error(1002, '请先预约')
    end

    redis:wj_close()

    return true
end

return _M
