# start containers
up:
	@./scripts/up.sh

# stop containers
down:
	@./scripts/down.sh

# run laravel horizon
horizon:
	@./scripts/horizon.sh

# run tests
test:
	@./scripts/test.sh
