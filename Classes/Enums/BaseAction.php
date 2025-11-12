<?php 

namespace MyApp\Enums;

enum BaseAction: string {
    case CREATE  = 'create';   // POST
    case GET     = 'get';      // GET (single)
    case GET_ = '';      // GET (single)
    case GET_ALL = 'all';  // GET (collection)
    case UPDATE  = 'update';   // PUT/PATCH
    case DELETE  = 'delete';   // DELETE

    public function allowedMethods(): array {
        return match($this) {
            self::CREATE  => ['POST'],
            self::GET     => ['GET'],
            self::GET_ALL => ['GET'],
            self::UPDATE  => ['PUT', 'PATCH'],
            self::DELETE  => ['DELETE'],
        };
    }
}
