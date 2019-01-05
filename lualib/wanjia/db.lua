--
-- 连接 数据库
--

local conf = require('wanjia.conf')

local _M = {}

-- 新建链接
-- dbname: 链接到哪个库
_M.new = function(dbname)
    local conns = {
        ['default'] = {
            ['host']        = conf.DB_HOST,
            ['port']        = conf.DB_PORT,
            ['database']    = conf.DB_DATABASE,
            ['user']        = conf.DB_USERNAME,
            ['password']    = conf.DB_PASSWORD
        },
        ['365jia'] = {
            ['host']        = conf.DB_365JIA_HOST,
            ['port']        = conf.DB_365JIA_PORT,
            ['database']    = conf.DB_365JIA_DATABASE,
            ['user']        = conf.DB_365JIA_USERNAME,
            ['password']    = conf.DB_365JIA_PASSWORD
        },
        ['365lin'] = {
            ['host']        = conf.DB_365LIN_HOST,
            ['port']        = conf.DB_365LIN_PORT,
            ['database']    = conf.DB_365LIN_DATABASE,
            ['user']        = conf.DB_365LIN_USERNAME,
            ['password']    = conf.DB_365LIN_PASSWORD
        },
    }

    if dbname == nil then
        dbname = 'default'
    end

    local mysql = require('resty.mysql').new()
    mysql:set_timeout(1000)
    mysql:connect(conns[dbname])


    mysql.wj_close = function(db)
        db:set_keepalive(10000, 200) -- 关闭链接
    end

    return mysql
end

return _M


