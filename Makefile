publish: ; docker-compose exec case_hello php publish.php
publish_all: ; make publish ; make publish_direct ; make publish_fanout ; make publish_topic
publish_direct: ; docker-compose exec case_direct php case/direct/publish.php
publish_fanout: ; docker-compose exec case_fanout php case/fanout/publish.php
publish_topic: ; docker-compose exec case_topic php case/topic/publish.php
down: ; docker-compose down
sdown: ; docker-compose down & rm -f *.txt
fdown: ; docker-compose down & rm -f *.txt & rm -rf vendor
upd: ; docker-compose up -d
upb: ; docker-compose up -d --build --remove-orphans
stats: ; docker ps -q | xargs  docker stats --no-stream
