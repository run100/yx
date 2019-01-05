--
-- Created by IntelliJ IDEA.
-- User: staff
-- Date: 2017/12/15
-- Time: 上午9:14
-- To change this template use File | Settings | File Templates.
--

local context = {}


function context.get_project_id()
    return ngx.req.get_uri_args().proj
end

function context.get_register_type()
    return ngx.req.get_uri_args().type == 'team' and 'team' or 'single'
end

function context.get_project_info()
    local config = require('common.config')
    local redis_conn = require('wanjia.redis').new()
    local cjson = require('cjson')
    if context.project_info then
        return context.project_info
    end

    local project_id = context.get_project_id()
    if not project_id then
        return nil
    end

    local key = string.format(config.project_info_key, project_id)
    if redis_conn:exists(key) == 1 then
        local info = cjson.decode(redis_conn:get(key))
        context.project_info = info
        return info
    end

    if not config.query_if_absent then
        return nil
    end

    local db_conn = require('wanjia.db').new()

    local result = db_conn:query('select * from zt_project where id =' .. project_id)
    if #result == 0 then
        return nil
    end

    context.project_info = result[1]
    redis_conn:set(key, cjson.encode(context.project_info))
    return context.project_info

end

function context.get_registered()
    local project_id = context.get_project_id()
    if not project_id then
        return nil
    end

    local redis_conn = require('wanjia.redis').new()

    local config = require('common.config')
    local key = string.format(config.data_key, project_id)
    return redis_conn:hlen(key)
end


return context
