// document  文档 		文档流
// get  	 获取
// element   元素 标签
// By 		 通过
// Id
//
// 获取 id = mynum 的标签 (对象)
// js声明变量 格式:  var 变量名
var mynum = document.getElementById('quantity');

// 购买数量-减
function jian() {

    // mynum.value  就是  PHP中对象名->属性名  js中对象名.属性名
    // alert(mynum.value); // 弹窗
    if (mynum.value > 0) {
        mynum.value = mynum.value - 1;
    }

    if (mynum.value < 1) {
        mynum.value = 1;
    }
}


// 购买数量-加
function jia() {
    // js中 只有纯数字类型 才能相加, 其余用+ 都是拼接的意思
    // parseInt() 强制转换成 整型
    mynum.value = parseInt(mynum.value) + 1;
    // mynum.value = mynum.value + 1;
}

