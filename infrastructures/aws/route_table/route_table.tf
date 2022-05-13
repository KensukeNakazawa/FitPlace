resource "aws_route_table" "tfer--rtb-0108ffab34717cdc4" {
  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = "igw-06e9b05ccb2366c4b"
  }

  tags = {
    Name = "FitPlacePublic"
  }

  tags_all = {
    Name = "FitPlacePublic"
  }

  vpc_id = "${data.terraform_remote_state.vpc.outputs.aws_vpc_tfer--vpc-002db229cd6373519_id}"
}

resource "aws_route_table" "tfer--rtb-05772d7d7e237fca9" {
  vpc_id = "${data.terraform_remote_state.vpc.outputs.aws_vpc_tfer--vpc-002db229cd6373519_id}"
}

resource "aws_route_table" "tfer--rtb-60e28506" {
  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = "igw-04100560"
  }

  vpc_id = "${data.terraform_remote_state.vpc.outputs.aws_vpc_tfer--vpc-72c2cc15_id}"
}
