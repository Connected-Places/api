from troposphere import Join, Ref


def create_default_queue_name_variable(environment_parameter, uuid_parameter):
    return Join('-', ['default', Ref(environment_parameter), Ref(uuid_parameter)])


def create_notifications_queue_name_variable(environment_parameter, uuid_parameter):
    return Join('-', ['notifications', Ref(environment_parameter), Ref(uuid_parameter)])


def create_search_queue_name_variable(environment_parameter, uuid_parameter):
    return Join('-', ['search', Ref(environment_parameter), Ref(uuid_parameter)])


def create_uploads_bucket_name_variable(environment_parameter, uuid_parameter):
    return Join('-', ['uploads', Ref(environment_parameter), Ref(uuid_parameter)])


def create_api_launch_template_name_variable(environment_parameter):
    return Join('-', ['api-launch-template', Ref(environment_parameter)])


def create_docker_repository_name_variable(environment_parameter, uuid_parameter):
    return Join('-', ['api', Ref(environment_parameter), Ref(uuid_parameter)])


def create_api_log_group_name_variable(environment_parameter):
    return Join('-', ['api', Ref(environment_parameter)])


def create_queue_worker_log_group_name_variable(environment_parameter):
    return Join('-', ['queue-worker', Ref(environment_parameter)])


def create_scheduler_log_group_name_variable(environment_parameter):
    return Join('-', ['scheduler', Ref(environment_parameter)])


def create_api_task_definition_family_variable(environment_parameter):
    return Join('-', ['api', Ref(environment_parameter)])


def create_queue_worker_task_definition_family_variable(environment_parameter):
    return Join('-', ['queue-worker', Ref(environment_parameter)])


def create_scheduler_task_definition_family_variable(environment_parameter):
    return Join('-', ['scheduler', Ref(environment_parameter)])


def create_api_user_name_variable(environment_parameter):
    return Join('-', ['api', Ref(environment_parameter)])


def create_ci_user_name_variable(environment_parameter):
    return Join('-', ['ci-api', Ref(environment_parameter)])


def create_database_name_variable():
    return 'healthy_london_partnership'


def create_database_username_variable():
    return 'healthy_london_partnership'


def create_elasticsearch_domain_name_variable(environment_parameter):
    return Join('-', ['search', Ref(environment_parameter)])
