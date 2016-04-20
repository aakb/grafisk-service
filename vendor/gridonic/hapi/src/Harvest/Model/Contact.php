<?php
/*
 * copyright (c) 2009 MDBitz - Matthew John Denton - mdbitz.com
 *
 * This file is part of HarvestAPI.
 *
 * HarvestAPI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * HarvestAPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HarvestAPI. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Harvest\Model;

/**
 * Contact
 *
 * This file contains the class Contact
 *
 * @author Matthew John Denton <matt@mdbitz.com>
 * @package com.mdbitz.harvest
 */

/**
 * Harvest Contact Object
 *
 * <b>Properties</b>
 * <ul>
 *   <li>client-id</li>
 *   <li>email</li>
 *   <li>fax</li>
 *   <li>first-name</li>
 *   <li>id</li>
 *   <li>last-name</li>
 *   <li>phone-mobile</li>
 *   <li>phone-office</li>
 *   <li>title</li>
 * </ul>
 *
 * @package com.mdbitz.harvest
 */
class Contact extends Harvest
{
    /**
     * @var string contact
     */
    protected $_root = "contact";

}
