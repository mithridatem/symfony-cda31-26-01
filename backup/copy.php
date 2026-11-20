<?php
class User{
    /* public function findOneUserListDTOById(int $id): ?UserListDTO
    {
        return $this->createQueryBuilder('u')
            ->select(
                'NEW App\DTO\UserListDTO(
                    u.firstname,
                    u.lastname,
                    u.email
                )'
            )
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllUserListDTO(): array 
    {
        return $this->createQueryBuilder('u')
            ->select(
                'NEW App\DTO\UserListDTO(
                    u.firstname,
                    u.lastname,
                    u.email
                )'
            )
            ->getQuery()
            ->getResult();
    } */
}
