/* This file contains common JS functionalities for All project HTML files */

/*(function () {
  
  // Shorter debug function
  function log(msg) {
    console.log(msg);
  }
  
  var n1 = 1;
  
  function sum(n2) { 
	n2++
	return n2;
  }
  
  log(sum(n1))
  
})()*/

$(document).ready(function() { 
	$('.bannerHolderImg').cycle({
		fx:      'scrollRight', 
		speed:    300, 
		timeout:  2000 
	});
});
