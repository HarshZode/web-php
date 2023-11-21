pipeline {
    agent any
   
    environment {
        DOCKER_IMAGE_NAME = 'harshzodedestek/www-php'
        DOCKER_CONTAINER_NAME = 'php-web-server'
        RELEASE_NUMBER = '1.0'
    }

    stages {
        stage('Git Pull') {
            steps {
              git branch: 'main', url: 'https://github.com/HarshZode/web-php'
            }
        }
        stage('Build Docker Image on Jenkins Server') {
            steps {
                sh "sed -i 's#build:#image: ${DOCKER_IMAGE_NAME}:${RELEASE_NUMBER}.${BUILD_NUMBER}#g' docker-compose.yml"
                sh "sed -i '/context:/d' docker-compose.yml"
                sh "sed -i '/dockerfile:/d' docker-compose.yml"
                sh 'docker build -t ${DOCKER_IMAGE_NAME}:${RELEASE_NUMBER}.${BUILD_NUMBER} .'
            }
        }
        
        stage('Build for testing ') {
            steps {
             sh 'docker stack deploy --compose-file docker-compose.yml php_mysql_stack || docker service update --image ${DOCKER_IMAGE_NAME}:${RELEASE_NUMBER}.${BUILD_NUMBER} php_mysql_stack_www'
            }
        }
        
        // stage ('Maven Test') {
        //     steps {
        //         build job: 'Maven-Test-STEP', wait: true
        //     }
        // }
        stage('Upload docker image to Docker Hub'){
             steps {
              withCredentials([string(credentialsId: 'harshzodedestek_Docker_hub', variable: 'harshzodedestek_Docker_hub')]) {
                 sh 'docker login -u harshzodedestek -p ${harshzodedestek_Docker_hub}'
                }
                sh 'docker push ${DOCKER_IMAGE_NAME}:${RELEASE_NUMBER}.${BUILD_NUMBER}'
                sh 'docker build -t ${DOCKER_IMAGE_NAME}:latest . '
                sh 'docker push ${DOCKER_IMAGE_NAME}:latest'
            }
        }
        stage('Prepare for Production Deployment') {
            steps {
                script {
                    // Update docker-compose.yml to use the image from the registry with the new tag for the 'www' service
                    // sh "sed -i 's#build:#image: ${DOCKER_IMAGE_NAME}:${RELEASE_NUMBER}.${BUILD_NUMBER}#g' docker-compose.yml"
                    // sh "sed -i '/context:/d' docker-compose.yml"
                    // sh "sed -i '/dockerfile:/d' docker-compose.yml"
                    sh "sed -i 's/replicas: 1/replicas: 1/g' docker-compose.yml"
                }
            }
        }
        stage('Deploy to Production Server') {
    steps {
        script {
            // Ensure the php-stack directory exists on the deployment server
            sshagent(['Docker_Master_VM']) {
                sh 'ssh -o StrictHostKeyChecking=no progainz1@10.128.0.7 "mkdir -p php-stack"'
            }

            // Copy the updated docker-compose.yml to the production server
            sshagent(['Docker_Master_VM']) {
                sh 'scp docker-compose.yml progainz1@10.128.0.7:php-stack/docker-compose.yml'
            }

            // SSH into the production server and deploy using Docker Swarm
            sshagent(['Docker_Master_VM']) {
               sh '''
                            ssh -o StrictHostKeyChecking=no progainz1@10.128.0.7 "
                                cd php-stack
                                docker stack deploy --compose-file docker-compose.yml php_mysql_stack || docker service update --image ${DOCKER_IMAGE_NAME}:${RELEASE_NUMBER}.${BUILD_NUMBER} php_mysql_stack_www
                              
                            "
                        '''
            }
        }
    }
}

    }
        
    
}