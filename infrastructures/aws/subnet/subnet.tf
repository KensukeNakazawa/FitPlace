resource "aws_subnet" "tfer--subnet-06fe6e9a64eba94b3" {
  assign_ipv6_address_on_creation = "false"
  cidr_block                      = "10.0.2.0/24"
  map_customer_owned_ip_on_launch = "false"
  map_public_ip_on_launch         = "false"

  tags = {
    Name = "FitPlacePrivate"
  }

  tags_all = {
    Name = "FitPlacePrivate"
  }

  vpc_id = "${data.terraform_remote_state.vpc.outputs.aws_vpc_tfer--vpc-002db229cd6373519_id}"
}

resource "aws_subnet" "tfer--subnet-09811307a61296e85" {
  assign_ipv6_address_on_creation = "false"
  cidr_block                      = "10.0.1.0/24"
  map_customer_owned_ip_on_launch = "false"
  map_public_ip_on_launch         = "false"

  tags = {
    Name = "FitPlaceWeb"
  }

  tags_all = {
    Name = "FitPlaceWeb"
  }

  vpc_id = "${data.terraform_remote_state.vpc.outputs.aws_vpc_tfer--vpc-002db229cd6373519_id}"
}
