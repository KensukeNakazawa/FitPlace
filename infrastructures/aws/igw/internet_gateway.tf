resource "aws_internet_gateway" "tfer--igw-06e9b05ccb2366c4b" {
  tags = {
    Name = "FitPlaceInternetGateWay"
  }

  tags_all = {
    Name = "FitPlaceInternetGateWay"
  }

  vpc_id = "${data.terraform_remote_state.vpc.outputs.aws_vpc_tfer--vpc-002db229cd6373519_id}"
}
