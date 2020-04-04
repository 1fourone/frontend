import requests
import json

def test(q):
    r = requests.get('http://localhost:8080/grader.php?type=test&question=' +  json.dumps(q['data']))
    reply = json.loads(r.text)
    assert q['data']['expectedOutput'] == reply, 'Grader differs from expected output'