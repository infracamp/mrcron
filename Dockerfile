FROM infracamp/kickstart-flavor-gaia:testing

ENV DEV_CONTAINER_NAME="mrcron"

ADD / /opt
RUN ["bash", "-c",  "chown -R user /opt"]
RUN ["/kickstart/flavorkit/scripts/start.sh", "build"]

HEALTHCHECK --interval=10s --timeout=3s --start-period=30s CMD curl -f http://localhost/ || exit 1

ENTRYPOINT ["/kickstart/flavorkit/scripts/start.sh", "standalone"]
