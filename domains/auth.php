<?php

namespace Domain;

interface IAuthUsecases
{
  public function authenticate(bool $redirect = true): User | null;
  public function signin(string $username, string $password);
  public function signout();
}
