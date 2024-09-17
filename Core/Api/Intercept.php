<?php
namespace Core\Api;

interface Intercept extends Component {
    public function intercept(&$event);
}
