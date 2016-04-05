Array.prototype.forEach.call(document.querySelectorAll('form'), function(el, i) {
    el.addEventListener('submit', handleVote);
});

function handleVote(e) {
    e.preventDefault();

    var button = e.target.querySelector('button');
    button.disabled = true;

    var data = getFormData(e.target);
    var alert = e.target.querySelector('.alert');

    if (data.gametype !== 'special') {
        if (data.maps[0].length !== 4 || data.maps[1].length !== 3) {
            setAlertStatus(alert, 'danger', 'You have to pick 4 old maps and 3 new.')
            button.disabled = false;
            return;
        }
    }

    var request = new XMLHttpRequest();
    request.open('POST', e.target.getAttribute('action'), true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

    request.onload = function() {
        if (this.status >= 200 && this.status < 400) {
            var res = JSON.parse(this.response);

            if (res.error !== undefined) {
                setAlertStatus(alert, 'danger', res.error);
                button.disabled = false;
            } else {
                setAlertStatus(alert, 'success', 'Thanks for vote');
            }
        } else {
            setAlertStatus(alert, 'danger', 'There was some sort of error. Try again later');
            button.disabled = false;
        }
    };

    request.send('data=' + JSON.stringify(data));
}

function setAlertStatus(el, status, text)
{
    el.classList.remove('alert-info', 'alert-success', 'alert-danger');
    el.classList.add('alert-' + status);
    el.innerHTML = text;
}

function getFormData(form) {
    var data = {
        maps: {
            0: [],
            1: []
        },
        gametype: null
    };

    var name;

    for (var i = 0; i < form.length; i++) {
        name = form[i].getAttribute('name');

        if (name === 'maps[0][]' && form[i].checked) {
            data.maps[0].push(form[i].value);
        } else if (name === 'maps[1][]' && form[i].checked) {
            data.maps[1].push(form[i].value);
        } else if (name === 'maps[2][]' && form[i].checked) {
            data.maps[2] = [];
            data.maps[2].push(form[i].value);
        } else if (name === 'gametype') {
            data.gametype = form[i].value;
        }
    }

    return data;
}
