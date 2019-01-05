--
-- 组版区块微服务 Yar
--


local cjson = require('cjson')
local yar = require('wanjia.yar')
local db = require('wanjia.db').new('365jia')
local redis = require('wanjia.redis').new()
local unserialize = require('phpunserialize').unserialize

local req = yar.parse()

local api = {}

api.read = function(block_id, type, ttl)
    if block_id == nil then
        return nil
    end


    ttl = ttl or 60
    type = type or 0

    local key = 'blk:' .. block_id .. '_' .. type .. '_' .. ttl

    local cache = redis:get(key)
    if cache == ngx.null then
        cache = api.readRaw(block_id, type)
        redis:setex(key, ttl, cjson.encode(cache))
    else
        cache = cjson.decode(cache)
    end


    redis:wj_close()

    return cache
end

api.readRaw = function(block_id, type)
    if block_id == nil then
        return nil
    end

    type = type or 0

    local limit = 1000

    -- 查数据库
    local sql = string.format([[
            select id, type, data, seq, updated_at, created_at
            from t_custom_block_item
            where custom_block_id = %d
            order by type, seq
            limit %d
        ]], block_id, limit)

    local ret = db:query(sql)
    db:wj_close()


    local filtered = {}
    for k, v in pairs(ret) do
        -- 解析 data 字段
        local data = unserialize(v.data)
        v.data = nil
        for k1, v1 in pairs(data) do
            v[k1] = v1
        end

        -- 解析 title 中的#字段到 extras
        local pos = v.title:find('#')
        if pos then
            v.clean_title = v.title:sub(1, pos - 1)
            v.extras = {}

            local extras = v.title:sub(pos)
            for i in extras:gmatch('#([^#]+)') do
                table.insert(v.extras, i)
            end
        end

        -- 转换 type 数字到字符串
        if v.type == 1 then
            v.stype = 'images'
        elseif v.type == 2 then
            v.stype = 'captions'
        elseif v.type == 4 then
            v.stype = 'subcaptions'
        elseif v.type == 8 then
            v.stype = 'intros'
        elseif v.type == 16 then
            v.stype = 'lists'
        elseif v.type == 32 then
            v.stype = 'votes'
        elseif v.type == 64 then
            v.stype = 'catlists'
        elseif v.type == 128 then
            v.stype = 'iconlists'
        end

        -- type 按位选择
        local checked = 0
        if type > 0 then
            if bit.band(type, v.type) > 0 then
                checked = 1
            end
        else
            checked = 1
        end

        if checked == 1 then
            if filtered[v.stype] == nil then
                filtered[v.stype] = {}
            end
            table.insert(filtered[v.stype], v)
        end

    end

    return filtered
end

api.invalidate = function(block_id, type, ttl)
    local key = 'blk:' .. block_id .. '_' .. type .. '_' .. ttl
    redis:del(key)
end


local cmd = api[req.method]
local ret = cmd(unpack(req.args))
yar.response(req, ret)
