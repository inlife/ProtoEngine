<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: async
 *  @Project: Proto Engine 3
 */

require("project.php");

import("providers.peAsync");

peAsync::main(
    peController::getData()
);