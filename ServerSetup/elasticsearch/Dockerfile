FROM docker.elastic.co/elasticsearch/elasticsearch:6.6.1

ADD . .

RUN cp elasticsearch.yml /usr/share/elasticsearch/config/
#RUN groupadd -g 999 elastic && \
#	useradd -r -u 999 -g elastic elastic

#USER elastic

CMD /usr/share/elasticsearch/bin/elasticsearch
