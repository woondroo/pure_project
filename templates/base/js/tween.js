(function(){
window.tween = function(startProps, endProps, timeSeconds, animType, delay)
{
	var tw = new Tween();
	tw.start(startProps, endProps, timeSeconds, animType, delay);
	return tw;
}

function Tween()
{
	this._frame=20;
	
	this._animType = linear;
	this._delay = 0;
	
	this.run = function(){}
	this.complete = function(){}
}

Tween.prototype.getValue = function(prop)
{
	this._valueType = '';
	if(prop.constructor == Array) return prop;
	
	if(typeof(prop) == 'string')
	{
		if(isColor(prop))
		{
			this._valueType = 'color';
			return c2a(prop);
		}
		if(prop.split('px').length>1)
		{
			this._valueType = 'px';
			return [prop.split('px')[0]];
		}
	}
	return [prop];
}
Tween.prototype.setValue = function(prop)
{
	if(this._valueType == 'color')return a2c(prop);
	if(this._valueType == 'px')return prop[0]+'px';
	return prop;
}

Tween.prototype.start = function(startProps, endProps, timeSeconds, animType, delay)
{
		if(animType != undefined)this._animType = this.animTypes[animType];
		if(delay != undefined)this._delay = delay;
		//
		this._timeSeconds = timeSeconds;
		this._startTimer = new Date().getTime() + this._delay * 1000;
		//
		this._endProps = this.getValue(endProps);
		this._startProps = this.getValue(startProps);
		this._currentProps = [];
		//
		var $this = this;
		clearInterval(this._runID);
		this._runID = setInterval(
			function(){$this._run();}
		,this._frame);
}

Tween.prototype.stop = function(state)
{
	for(var i in this._startProps)
	{
		if(Number(state)>0)
		this._currentProps[i] = this._endProps[i];
		else if(Number(state)<0)
		this._currentProps[i] = this._startProps[i];
	}
	this.callListener();
	this.complete();
	//
	clearInterval(this._runID);
}
Tween.prototype.callListener = function()
{
	this.run(this.setValue(this._currentProps));
}
Tween.prototype._run = function()
{
	if ( new Date().getTime()- this._startTimer< 0) return;
	var isEnd = false;
	//
	for(var i in this._startProps)
	{
		this._currentProps[i] = this._animType( new Date().getTime()-this._startTimer,Number(this._startProps[i]),Number(this._endProps[i])-Number(this._startProps[i]),this._timeSeconds * 1000);
		//
		if(this._startTimer + (this._timeSeconds * 1000) <= new Date().getTime())
		{
			this._currentProps[i] = this._endProps[i];
			isEnd = true;
		}
	}
	//
	if(isEnd)this.stop();
	else this.callListener();
}


//{types
function linear(t,b,c,d)
{
	// simple linear tweening - no easing
	return c * t / d + b;
}
function easeinquad(t,b,c,d)
{
	// quadratic (t^2) easing in - accelerating from zero velocity
	return c * (t /= d) * t + b;
}

function easeoutquad(t,b,c,d)
{
	// quadratic (t^2) easing out - decelerating to zero velocity
	return -c * (t /= d) * (t - 2) + b;
}
function easeinoutquad(t,b,c,d)
{
	// quadratic (t^2) easing in/out - acceleration until halfway, then deceleration
	if ((t/=d/2) < 1) return c/2*t*t + b;
	return -c / 2 * ((--t) * (t - 2) - 1) + b;
}
function easeincubic(t,b,c,d)
{
	// cubic (t^3) easing in - accelerating from zero velocity
	return c * (t /= d) * t * t + b;
}
function easeoutcubic(t,b,c,d)
{
	// cubic (t^3) easing out - decelerating to zero velocity
	return c * ((t = t / d - 1) * t * t + 1) + b;
}

function easeinoutcubic(t,b,c,d)
{
	// cubic (t^3) easing in/out - acceleration until halfway, then deceleration
	if ((t/=d/2) < 1) return c / 2 * t * t * t + b;
	return c / 2 * ((t -= 2) * t * t + 2) + b;
}
function easeinquart(t,b,c,d)
{
	// quartic (t^4) easing in - accelerating from zero velocity
	return c * (t /= d) * t * t * t + b;
}
function easeoutquart(t,b,c,d)
{
	// quartic (t^4) easing out - decelerating to zero velocity
	return -c * ((t = t / d - 1) * t * t * t - 1) + b;
}
function easeinoutquart(t,b,c,d)
{
	// quartic (t^4) easing in/out - acceleration until halfway, then deceleration
	if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
	return -c / 2 * ((t -= 2) * t * t * t - 2) + b;
}
function easeinquint(t,b,c,d)
{
	// quintic (t^5) easing in - accelerating from zero velocity
	return c * (t /= d) * t * t * t * t + b;
}
function easeoutquint(t,b,c,d)
{
	// quintic (t^5) easing out - decelerating to zero velocity
	return c * ((t = t / d - 1) * t * t * t * t + 1) + b;
}
function easeinoutquint(t,b,c,d)
{
	// quintic (t^5) easing in/out - acceleration until halfway, then deceleration
	if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
	return c/2*((t-=2)*t*t*t*t + 2) + b;
}
function easeinsine(t,b,c,d)
{
	// sinusoidal (sin(t)) easing in - accelerating from zero velocity
	return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
}
function easeoutsine(t,b,c,d)
{
	// sinusoidal (sin(t)) easing out - decelerating to zero velocity
	return c * Math.sin(t/d * (Math.PI/2)) + b;
}
function easeinoutsine(t,b,c,d)
{
	// sinusoidal (sin(t)) easing in/out - acceleration until halfway, then deceleration
	return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
}
function easeinexpo(t,b,c,d)
{
	// exponential (2^t) easing in - accelerating from zero velocity
	return (t == 0) ? b : c * Math.pow(2, 10 * (t / d - 1)) + b;
}
function easeoutexpo(t,b,c,d)
{
	// exponential (2^t) easing out - decelerating to zero velocity
	return (t == d) ? b + c : c * ( -Math.pow(2, -10 * t / d) + 1) + b;
}
function easeinoutexpo(t,b,c,d)
{
	// exponential (2^t) easing in/out - acceleration until halfway, then deceleration
	if (t==0) return b;
	if (t==d) return b+c;
	if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
	return c / 2 * ( -Math.pow(2, -10 * --t) + 2) + b;
}
function easeincirc(t,b,c,d)
{
	// circular (sqrt(1-t^2)) easing in - accelerating from zero velocity
	return -c * (Math.sqrt(1 - (t /= d) * t) - 1) + b;
}
function easeoutcirc(t,b,c,d)
{
	// circular (sqrt(1-t^2)) easing out - decelerating to zero velocity
	return c * Math.sqrt(1 - (t = t / d - 1) * t) + b;
}
function easeinoutcirc(t,b,c,d)
{
	// circular (sqrt(1-t^2)) easing in/out - acceleration until halfway, then deceleration
	if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
	return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
}
function easeinelastic(t,b,c,d)
{
	var s=1,a=1,p=0;
	// elastic (exponentially decaying sine wave)
	if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
	if (a < Math.abs(c)) { a=c; s=p/4; }
	else s = p/(2*Math.PI) * Math.asin (c/a);
	return -(a * Math.pow(2, 10 * (t -= 1)) * Math.sin( (t * d - s) * (2 * Math.PI) / p )) + b;
}
function easeoutelastic(t,b,c,d)
{
	var s=1,a=1,p=0;
	// elastic (exponentially decaying sine wave)
	if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
	if (a < Math.abs(c)) { a=c; s=p/4; }
	else s = p/(2*Math.PI) * Math.asin (c/a);
	return a * Math.pow(2, -10 * t) * Math.sin( (t * d - s) * (2 * Math.PI) / p ) + c + b;
}
function easeinoutelastic(t,b,c,d)
{
	var s=1,a=1,p=0;
	// elastic (exponentially decaying sine wave)
	if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
	if (a < Math.abs(c)) { a=c; s=p/4; }
	else s = p/(2*Math.PI) * Math.asin (c/a);
	if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	return a * Math.pow(2, -10 * (t -= 1)) * Math.sin( (t * d - s) * (2 * Math.PI) / p ) * .5 + c + b;
}
function easeinback(t,b,c,d)
{
	 var s;
// Robert Penner's explanation for the s parameter (overshoot ammount):
//  s controls the amount of overshoot: higher s means greater overshoot
//  s has a default value of 1.70158, which produces an overshoot of 10 percent
//  s==0 produces cubic easing with no overshoot
	// back (overshooting cubic easing: (s+1)*t^3 - s*t^2) easing in - backtracking slightly, then reversing direction and moving to target
	if (s == undefined) s = 1.70158;
	return c * (t /= d) * t * ((s + 1) * t - s) + b;
}
function easeoutback(t,b,c,d)
{
	var s;
	// back (overshooting cubic easing: (s+1)*t^3 - s*t^2) easing out - moving towards target, overshooting it slightly, then reversing and coming back to target
	if (s == undefined)  s = 1.70158;
	return c * ((t = t / d - 1) * t * ((s + 1) * t + s) + 1) + b;
}
function easeinoutback(t,b,c,d)
{
	var s;
	// back (overshooting cubic easing: (s+1)*t^3 - s*t^2) easing in/out - backtracking slightly, then reversing direction and moving to target, then overshooting target, reversing, and finally coming back to target
	if (s == undefined)  s = 1.70158; 
	if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
	return c / 2 * ((t -= 2) * t * (((s *= (1.525)) + 1) * t + s) + 2) + b;
}
function easeinbounce(t,b,c,d)
{
// This were changed a bit by me (since I'm not using Penner's own Math.* functions)
// So I changed it to call getValue() instead (with some different arguments)
	// bounce (exponentially decaying parabolic bounce) easing in
	return c - easeoutbounce (d-t,b,c,d) + b;
}
function easeoutbounce(t,b,c,d)
{
	// bounce (exponentially decaying parabolic bounce) easing out
	if ((t/=d) < (1/2.75)) {
	  return c*(7.5625*t*t) + b;
	} else if (t < (2/2.75)) {
	  return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
	} else if (t < (2.5/2.75)) {
	  return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
	} else {
	  return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
	}
}
function easeinoutbounce(t,b,c,d)
{
	// bounce (exponentially decaying parabolic bounce) easing in/out
	if (t < d/2) return easeinbounce (t*2,b,c,d) * .5 + b;
	return easeoutbounce(t*2-d,b,c,d) * .5 + c * .5 + b;
}
//}
Tween.prototype.animTypes ={linear:linear,easeinquad:easeinquad,easeoutquad:easeoutquad,easeinoutquad:easeinoutquad,easeincubic:easeincubic,easeoutcubic:easeoutcubic,easeinoutcubic:easeinoutcubic,easeinquart:easeinquart,easeoutquart:easeoutquart,easeinoutquart:easeinoutquart,easeinquint:easeinquint,easeoutquint:easeoutquint,easeinoutquint:easeinoutquint,easeinsine:easeinsine,easeoutsine:easeoutsine,easeinoutsine:easeinoutsine,easeinexpo:easeinexpo,easeoutexpo:easeoutexpo,easeinoutexpo:easeinoutexpo,easeincirc:easeincirc,easeoutcirc:easeoutcirc,easeinoutcirc:easeinoutcirc,easeinelastic:easeinelastic,easeoutelastic:easeoutelastic,easeinoutelastic:easeinoutelastic,easeinback:easeinback,easeoutback:easeoutback,easeinoutback:easeinoutback,easeinbounce:easeinbounce,easeoutbounce:easeoutbounce,easeinoutbounce:easeinoutbounce};


//{
function isColor(v)
{
	return v.split('rgb').length>1||v.split('#').length>1;
}
//
function c2a(c)
{
	/*  ·Çie£ºrgb(0,0,0) */
	if(c.split('rgb').length>1)
	{
		return  c.split('(')[1].split(')')[0].split(',');
	}
	/* ie #fff / #ffffff */
	if(c.split('#').length>1)c = c.split('#')[1];
	if(c.length == 3)
	{
		c = c.charAt(0)+c.charAt(0)+c.charAt(1)+c.charAt(1)+c.charAt(2)+c.charAt(2);
	}
	c = '0x'+c;
	return [Number((c>>16)&0xff),Number((c>>8)&0xff),Number(c&0xff)];
}
function a2c(arr)
{
	var c = ((parseInt(Math.abs(arr[0]))<<16)+(parseInt(Math.abs(arr[1]))<<8)+parseInt(Math.abs(arr[2]))).toString(16);
	while(c.length<6)c='0'+c;
	return '#'+c;
}
//}
})();