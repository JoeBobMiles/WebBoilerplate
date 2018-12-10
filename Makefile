
ROOT_DIR = /var/www
TEST_DIR = $(ROOT_DIR)/tests

TEST_BOOTSTRAP = $(TEST_DIR)/test_bootstrapper.php
TEST_ARGS = --bootstrap $(TEST_BOOTSTRAP) --testdox

test:
	@vagrant ssh -c "phpunit $(TEST_ARGS) $(TEST_DIR)"