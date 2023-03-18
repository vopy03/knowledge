function outNum(num, elem, time, step) {
			    let e = document.querySelector('#'+elem);
			    n = 0;
			    let t = Math.round(time/(num/step));
			    let interval = setInterval(() => {
			        n = n + step;
			        if(n == num) {
			              clearInterval(interval);
			         }
			    e.innerHTML = n+'%';
			                }, t);
};
function roundToTwo(num) {    
    return +(Math.round(num + "e+2")  + "e-2");
}