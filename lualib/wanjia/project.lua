--
-- Project 相关
--

local _M = {}

-- 取 Project 配置
-- id: project_id
_M.conf = function(id)
    if id == nil then
        return nil
    end

--    local cjson = require('cjson')
--    local redis = require('wanjia.redis').new()
    local ns = string.format("prj:%d", id)
--    local ret = redis:get(ns .. ':conf')
--    redis:wj_close()
--    ret = cjson.decode(ret)
--    ret.ns = ns
--    return ret
    return {["ns"] = ns}
end

return _M
