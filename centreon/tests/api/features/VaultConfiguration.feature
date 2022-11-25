Feature: Vault Configuration API
  In order to use Vault Configuration API
  As a logged user
  I need to be able to create, update, delete and retrieve Vault Configuration information

  Background:
    Given a running instance of Centreon Web API

  Scenario: Create a new vault configuration as an admin user
    Given I am logged in
    And the endpoints are described in Centreon Web API documentation (version: 23.04)
    When I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    Then the response code should be "201"

  Scenario: Create a new vault configuration as an admin user for vault provider that not exists
    Given I am logged in
    And the endpoints are described in Centreon Web API documentation (version: 23.04)
    When I send a POST request to '/api/latest/administration/vaults/2/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    Then the response code should be "404"

  Scenario: Create a new vault configuration as a non-admin user with rights to Reach API
    Given the following CLAPI import data:
    """
      CONTACT;ADD;kev;kev;kev@localhost;Centreon@2022;0;1;en_US;local
      CONTACT;setparam;kev;reach_api;1
    """
    And I am logged in with "kev"/"Centreon@2022"

    When I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    Then the response code should be "403"
    And the JSON should be equal to:
    """
      {
        "code": 403,
        "message": "Only admin user can create vault configuration"
      }
    """

  Scenario: Create a new vault configuration as a non-admin user without rights to Reach API
    Given the following CLAPI import data:
    """
      CONTACT;ADD;kev;kev;kev@localhost;Centreon@2022;0;1;en_US;local
      CONTACT;setparam;kev;reach_api;0
    """
    And I am logged in with "kev"/"Centreon@2022"

    When I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    Then the response code should be "403"

  Scenario: Create a new vault configuration as an admin user while the same vault configuration already exists
    Given I am logged in
    And I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """

    When I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myAnotherVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myAnotherRoleId",
        "secret_id": "myAnotherSecretId"
      }
    """
    Then the response code should be "400"

  Scenario: Create a new vault configuration as an admin user with invalid parameter
    Given I am logged in
    When I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    Then the response code should be "400"

  Scenario: Update an existing vault configuration as an admin user
    Given I am logged in
    And I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """

    When I send a PUT request to '/api/latest/administration/vaults/1/configurations/1' with body:
    """
      {
        "name": "myNewVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    Then the response code should be "204"

  Scenario: Update a vault configuration that does not exist as an admin user
    Given I am logged in
    When I send a PUT request to '/api/latest/administration/vaults/1/configurations/1' with body:
    """
      {
        "name": "myNewVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    Then the response code should be "404"

  Scenario: Update vault configuration as an admin user by setting address, port and storage to be the same as in another existing one
    Given I am logged in
    And I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    And I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myAnotherVaultConfiguration",
        "address": "127.0.0.2",
        "port": 8201,
        "storage": "myAnotherStorageFolder",
        "role_id": "myAnotherRoleId",
        "secret_id": "myAnotherSecretId"
      }
    """

    When I send a PUT request to '/api/latest/administration/vaults/1/configurations/1' with body:
    """
      {
        "name": "myNewVaultConfiguration",
        "address": "127.0.0.2",
        "port": 8201,
        "storage": "myAnotherStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    Then the response code should be "400"

  Scenario: Delete vault configuration as an admin user
    Given I am logged in
    And I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """

    When I send a DELETE request to '/api/latest/administration/vaults/1/configurations/1'
    Then the response code should be "204"

  Scenario: Delete vault configuration as a non-admin user
    Given I am logged in
    And I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """
    And the following CLAPI import data:
    """
      CONTACT;ADD;kev;kev;kev@localhost;Centreon@2022;0;1;en_US;local
      CONTACT;setparam;kev;reach_api;1
    """
    And I am logged in with "kev"/"Centreon@2022"

    When I send a DELETE request to '/api/latest/administration/vaults/1/configurations/1'
    Then the response code should be "403"

  Scenario: Delete vault configuration as an admin user while vault provider id does not exist
    Given I am logged in
    And I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """

    When I send a DELETE request to '/api/latest/administration/vaults/2/configurations/1'
    Then the response code should be "404"

  Scenario: Delete vault configuration as an admin user while vault configuration id does not exist
    Given I am logged in
    And I send a POST request to '/api/latest/administration/vaults/1/configurations' with body:
    """
      {
        "name": "myVaultConfiguration",
        "address": "127.0.0.1",
        "port": 8200,
        "storage": "myStorageFolder",
        "role_id": "myRoleId",
        "secret_id": "mySecretId"
      }
    """

    When I send a DELETE request to '/api/latest/administration/vaults/1/configurations/2'
    Then the response code should be "404"