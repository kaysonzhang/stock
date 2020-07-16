<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<!-- import Vue before Element -->
<script src="https://unpkg.com/vue@2.6.11/dist/vue.js"></script>
<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>

## 获取自选股,需要登录
https://stock.xueqiu.com/v5/stock/portfolio/stock/list.json?size=1000&category=1

## 获取股票相关信息
https://stock.xueqiu.com/v5/stock/quote.json?symbol=SZ002185&extend=detail

## 获取股票所在板块的相关股票
https://xueqiu.com/stock/industry/stockList.json?code=SZ300433&type=1&size=100


##获取所有历史数据
http://api.finance.ifeng.com/akdaily/?code=sh601066&type=last

##60分钟线数据
http://money.finance.sina.com.cn/quotes_service/api/jsonp_v2.php/a=/CN_MarketData.getKLineData?symbol=sh601066&scale=60&ma=no&datalen=1023