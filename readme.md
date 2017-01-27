# MES based on Laravel5.2 framework


MES(Manufacturing Execution System)即制造企业生产过程执行系统，是一套面向制造企业车间执行层的生产信息化管理系统。该项目是在本人工作经验的基础上使用laravel框架进行重写。

该系统主要模块包括面向生管，设备，制程及OP的功能，生管部分包括对工单的开立，run card的开立，下线，撤单，入库，站区WIP表等功能；设备部分包括机台建立，管理，机况查询，机台稼动统计等；制程整合部分包括制程管理，制程流程设定和产品制程设定。该系统还保留与EDC的交互功能。


## Update Info

2017/1/23 实现lot拆批功能，修改导航栏内容及排版，显示信息条数（没有内容），站区工作流内容页UI实现（未完成check in & check out）.

2017/1/22 在上个版本的基础上完成run card开立到create lot的实现，对新增工单使用validate.js进行表单验证.

2017/1/12 git到github.

## Comment

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
