<?php
// header("Access-Control-Allow-Origin: *");
// // header("Content-type:application/json",true);
// // header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// get all todos
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
// header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

$app->group('/api', function () use ($app) {
    $app->get('/todos', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT * FROM tasks ORDER BY id");
       $sth->execute();
       $todos = $sth->fetchAll();
       return $this->response->withJson($todos);
   });    

    // $app->get('/todos', function ($request, $response, $args) {
    //      $sth = $this->db->prepare("SELECT * FROM tasks ORDER BY task");
    //     $sth->execute();
    //     $todos = $sth->fetchAll();
    //     return $this->response->withJson($todos);
    // });

    // Retrieve todo with id 
    $app->get('/todo/[{id}]', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT * FROM tasks WHERE id=:id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $todos = $sth->fetchObject();
        return $this->response->withJson($todos);
    });


    // Search for todo with given search teram in their name
    $app->get('/todos/search/[{query}]', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT * FROM tasks WHERE UPPER(task) LIKE :query ORDER BY task");
        $query = "%".$args['query']."%";
        $sth->bindParam("query", $query);
        $sth->execute();
        $todos = $sth->fetchAll();
        return $this->response->withJson($todos);
    });

    // Add a new todo
    // $app->post('/todo', function ($request, $response) {
    //     $input = $request->getParsedBody();
    //     $sql = "INSERT INTO tasks (task) VALUES (:task)";
    //      $sth = $this->db->prepare($sql);
    //     $sth->bindParam("task", $input['task']);
    //     $sth->execute();
    //     $input['id'] = $this->db->lastInsertId();
    //     return $this->response->withJson($input);
    // });
        
    $app->post('/todo', function ($request, $response) {
        $input = $request->getParsedBody();
        $sql = "INSERT INTO tasks (task, status, created_at) VALUES (:task, :status, :created_at)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("task", $input['task']);
        
        $sth->bindParam("status", $input['status']);
        $sth->bindParam("created_at", $input['created_at']);        

        $sth->execute();
        $input['id'] = $this->db->lastInsertId();
        return $this->response->withJson($input);
    });    

    // DELETE a todo with given id
    $app->delete('/todo/[{id}]', function ($request, $response, $args) {
        $sth = $this->db->prepare("DELETE FROM tasks WHERE id=:id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $todos = $sth->fetchAll();
        return $this->response->withJson($todos);
    });

    // Update todo with given id
    $app->put('/todo/[{id}]', function ($request, $response, $args) {
        response.setHeader('Access-Control-Allow-Origin', '*');
        response.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
        response.setHeader('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        $input = $request->getParsedBody();
        $sql = "UPDATE tasks SET task=:task, status=:status, created_at=:created_at WHERE id=:id";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("id", $args['id']);
        $sth->bindParam("task", $input['task']);
        $sth->bindParam("status", $input['status']);
        $sth->bindParam("created_at", $input['created_at']);
        $sth->execute();
        $input['id'] = $args['id'];
        return $this->response->withJson($input);
    });
});