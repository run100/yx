--
-- Created by IntelliJ IDEA.
-- User: staff
-- Date: 2017/12/8
-- Time: 上午10:12
-- To change this template use File | Settings | File Templates.
--
_M = {}
_M.update_participant = function(temp, project_id, uniqid, config)

    local keys = {}
    local db = require('wanjia.db').new()
    local result = db:query(string.format("select info from zt_player where project_id=%d and uniqid='%s'", project_id, uniqid))
    local participant = result[1]
    if not participant then
        return false, {code = 34, msg='not found'}
    end

    local json = require('cjson')

    local info = json.decode(participant.info)

    for k, v in pairs(config.base_form_design) do
        if v.confirm then
            if temp[v.field] then
                info[v.field] = temp[v.field]
                if v.key
                        and type(v.key) ~= 'userdata'
                then
                    table.insert(keys, v.key .. "='" .. temp[v.field] .. "'")
                end

            elseif v.required then
                return false, {code = 34, msg = v.name .. 'is required'}
            end
        end
    end

    local updated_at = os.date('%Y-%m-%d %H:%M:%S')
    local sql = string.format("update zt_player set info = '%s', updated_at='%s'", json.encode(info), updated_at)

    if #keys > 0 then
        sql = sql .. ',' .. table.concat(keys, ',')
    end

    sql = sql .. string.format(" where project_id=%d and uniqid='%s'", project_id, uniqid)

    local ret, err = db:query(sql)
    if err then
        return false, {code=34, msg=err}
    end

    return ret
end


_M.save_participant = function(context, temp)
    local cjson = require('cjson')
    local errors = require('common.errors')
    local config = cjson.decode(context.get_project_info().configs)
    local project_id = context.get_project_id();
    local info = {}
    local data = {}
    local redis = require('wanjia.redis')
    local uniqid
    for k, v in pairs(config.base_form_design) do
        if v.registration then
            if temp[v.field] then
                info[v.field] = temp[v.field]
                if v.key
                        and type(v.key) ~= 'userdata'
                then
                    data[v.key] = temp[v.field]
                    if v.key == 'uniqid' then
                        uniqid = temp[v.field]
                    end

                end

            elseif v.required then
                return false, errors.error(errors.FIELD_MISSING, '缺少' .. v.field)
            end
        end
    end

    if not uniqid then
        return false, errors.error(errors.FIELD_MISSING, '缺少唯一ID')
    end



    data.info = info
    data.created_at = os.date('%Y-%m-%d %H:%M:%S')
    data.updated_at = os.date('%Y-%m-%d %H:%M:%S')
    data.project_id = project_id
    local auto_number = config.baoming.ticket_mode == 'auto'
    return redis.exec_lua('baoming', 'register', 'prj:' .. project_id, uniqid, cjson.encode(data), auto_number)
--return redis.exec_lua('baoming', 'book', 'prj:1', uniqid, 1)

end

return _M
