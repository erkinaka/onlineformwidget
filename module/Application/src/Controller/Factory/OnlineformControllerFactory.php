<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Application\Controller\OnlineformController;

/**
 * Description of OnlineformFactory
 *
 * @author Erkin
 */
class OnlineformControllerFactory {
      public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

            $table=$container->get('Application\Model\OnlineformTable');

        return new OnlineformController($table);
    }

}
