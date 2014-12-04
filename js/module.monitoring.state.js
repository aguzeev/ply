$(document).ready(function(e) {
    state_in();
	state_out();
});

function state_in() {
	var socket;
	var echoStart = new Date().getTime();

    var host = "ws://188.226.151.100:8765/fanera/ws/warmer_in/";
    socket = new WebSocket(host);

    socket.onopen = function() {
        document.getElementById('state_in').innerHTML= 'Connected';
        socket.send(JSON.stringify({type: 'echo'}));
    }

    socket.onmessage = function(msg){
        obj = JSON.parse(msg.data);

        if(obj.type == 'realtime_data')
        {
            var d = new Date(obj.current_list.timestamp);
            var utc = d.getTime() + (d.getTimezoneOffset() * 60000);
            var dt = new Date(utc);
             var hoursValue = dt.getHours();
             var minutesValue = dt.getMinutes();
             var secondsValue = dt.getSeconds();

             if ( hoursValue < 10 )
              hoursValue = '0' + hoursValue;
             if ( minutesValue < 10 )
              minutesValue = '0' + minutesValue;
             if ( secondsValue < 10 )
              secondsValue = '0' + secondsValue;

            var dateStr = 
            hoursValue + ":" +
            minutesValue + ":" +
            secondsValue;
            var ts = " (" + dateStr + ")";
            if("square" in obj.current_list)
                document.getElementById('sq_span_in').innerHTML=obj.current_list.square.toFixed(2) + ts;
            if("CPU_temperatures" in obj.current_list)
            {
                var a = 0;
                for (var i = 0; i < obj.current_list.CPU_temperatures.length; i++)
                {
                    a += parseFloat(obj.current_list.CPU_temperatures[i]);
                }
                a /= obj.current_list.CPU_temperatures.length;
                document.getElementById('cpu_temp_span_in').innerHTML= a + String.fromCharCode(8451) + ts;
            }
        }
        else if(obj.type == 'echo')
        {
            var delay = new Date().getTime() - echoStart;
            document.getElementById('pp_delay_in').innerHTML=delay + " ms";
        }
    }

    socket.onclose = function(){
        document.getElementById('state_in').innerHTML = 'Closed';
    }           
};

function state_out() {
	var socket;
	var echoStart = new Date().getTime();
    
    var host = "ws://188.226.151.100:8765/fanera/ws/warmer_out/";
    socket = new WebSocket(host);

    socket.onopen = function() {
        document.getElementById('state_out').innerHTML= 'Connected';
        socket.send(JSON.stringify({type: 'echo'}));
    }

    socket.onmessage = function(msg){
        obj = JSON.parse(msg.data);

        if(obj.type == 'realtime_data')
        {
            var d = new Date(obj.current_list.timestamp);
            var utc = d.getTime() + (d.getTimezoneOffset() * 60000);
            var dt = new Date(utc);

             var hoursValue = dt.getHours();
             var minutesValue = dt.getMinutes();
             var secondsValue = dt.getSeconds();

             if ( hoursValue < 10 )
              hoursValue = '0' + hoursValue;
             if ( minutesValue < 10 )
              minutesValue = '0' + minutesValue;
             if ( secondsValue < 10 )
              secondsValue = '0' + secondsValue;

            var dateStr = 
            hoursValue + ":" +
            minutesValue + ":" +
            secondsValue;
            var ts = " (" + dateStr + ")";
            if("width" in obj.current_list)
                //document.getElementById('width_span_out').innerHTML=obj.current_list.width.toFixed(2) + ts;
            if("height" in obj.current_list)
                //document.getElementById('height_span_out').innerHTML=obj.current_list.height.toFixed(2) + ts;
            if("koef30min" in obj.current_list)
                document.getElementById('koef30min_span_out').innerHTML=obj.current_list.koef30min.toFixed(2) + ts;
            if("CPU_temperatures" in obj.current_list)
            {
                var a = 0;
                for (var i = 0; i < obj.current_list.CPU_temperatures.length; i++)
                {
                    a += parseFloat(obj.current_list.CPU_temperatures[i]);
                }
                a /= obj.current_list.CPU_temperatures.length;
                document.getElementById('cpu_temp_span_out').innerHTML=a + String.fromCharCode(8451) + ts;
            }
        }
        else if(obj.type == 'echo')
        {
            var delay = new Date().getTime() - echoStart;
            document.getElementById('pp_delay_out').innerHTML=delay + " ms";
        }
    }

    socket.onclose = function(){
        document.getElementById('state_out').innerHTML = 'Closed';
    }           
};