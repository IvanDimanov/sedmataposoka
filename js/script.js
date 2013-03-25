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

    $(function() {
      $('#slides').slidesjs({
        width: 488,
        height: 164,
        play: {
          active: true,
          auto: true,
          interval: 4000,
          swap: true
        }
      });
    });