import { Field, ID, ObjectType } from '@nestjs/graphql';

@ObjectType()
export class wp_users {
  @Field(() => ID)
  id: number;
  user_login: string;
  user_url: string;
}