function parseAmiResponse(res) {

	var result = new Object();
	if (res['type'] == 'queueStatus') {
		var events = res['data']['events'];
		var e;
		var qnum;

		result['all'] = new Object();		
		result['all']['members'] = new Array();
		result['all']['entries'] = new Array();
		for (i=0; i< events.length; i++) {
			e = events[i];
			qnum = e['queue'];
			if (e['event'] == 'QueueParams') {
				result[qnum] = e;
				result[qnum]['members'] = new Array();
				result[qnum]['entries'] = new Array();
			}
			if (e['event'] == 'QueueMember') {
				result[qnum]['members'].push(e);
				result['all']['members'].push(e);
				
			}
			if (e['event'] == 'QueueEntry') {
				result[qnum]['entries'].push(e);
				result['all']['entries'].push(e);
			}			
		}
	}
	else {
		return false;
	}

	return result;
}