<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: index
 *  @Project: Proto Engine 3
 */
require("project.php");

peLoader::import("providers.peController");
peLoader::import("providers.peTemplate");

peTemplate::main(
    peController::getData()
);